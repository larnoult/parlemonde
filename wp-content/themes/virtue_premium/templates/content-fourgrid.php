<?php 
    global $post, $virtue_premium; 

    $postsummery = get_post_meta( $post->ID, '_kad_post_summery', true );
    $image_size = 364;
    $image_height = apply_filters('kadence_blog_grid_image_height', null);
    if($image_height == null) {
        $image_slider_height = $image_size;
    } else {
        $image_slider_height = $image_height;
    }

    if(empty($postsummery) || $postsummery == 'default') {
        if(!empty($virtue_premium['post_summery_default'])) {
            $postsummery = $virtue_premium['post_summery_default'];
        } else {
            $postsummery = 'img_portrait';
        }
    }
    if ( 'img_landscape' == $postsummery || 'img_portrait' == $postsummery ) {
		$img_args = array(
			'width'           => $image_size,
			'height'          => $image_height,
			'crop'            => true,
			'class'           => 'attachment-thumb wp-post-image kt-image-intrinsic',
			'alt'             => null,
			'id'              => get_post_thumbnail_id( $post->ID ),
			'placeholder'     => true,
			'schema'          => true,
			'intrinsic'       => true,
			'intrinsic_max'   => true,
		); ?>
        <div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in kad_blog_fade_in grid_item" itemscope="" itemtype="http://schema.org/BlogPosting">
            <div class="imghoverclass img-margin-center">
                <a href="<?php the_permalink()  ?>" title="<?php the_title_attribute(); ?>">
                	<?php virtue_print_full_image_output( $img_args ); ?>
                </a> 
            </div>
          <?php $image = null; $thumbnailURL = null; 

    } else if( $postsummery == 'slider_landscape' || $postsummery == 'slider_portrait' ) {?>
		<div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in kad_blog_fade_in grid_item" itemscope="" itemtype="http://schema.org/BlogPosting">
			<?php
			$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
			virtue_build_slider($post->ID, $image_gallery, $image_size, $image_slider_height, 'post', 'kt-slider-same-image-ratio');

    } else if( $postsummery == 'video' ) {?>
        <div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in kad_blog_fade_in grid_item" itemscope="" itemtype="http://schema.org/BlogPosting">
            <div class="videofit">
                <?php
                echo do_shortcode( get_post_meta( $post->ID, '_kad_post_video', true ) );?>
            </div>

    <?php 
    } else {?>
        <div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in kad_blog_fade_in grid_item kt-no-post-summary" itemscope="" itemtype="http://schema.org/BlogPosting">
    <?php } ?>

            <div class="postcontent">
                <?php 
                /**
                * @hooked virtue_post_before_header_meta_date - 20
                */
                do_action( 'kadence_post_grid_excerpt_before_header' );
                ?>
                <header>
                    <?php 
                    /**
                    * @hooked virtue_post_mini_excerpt_header_title - 10
                    * @hooked virtue_post_grid_header_meta - 20
                    */
                    do_action( 'kadence_post_grid_small_excerpt_header' );
                    ?>      
                </header>
                <div class="entry-content" itemprop="articleBody">
                    <?php 
                     do_action( 'kadence_post_grid_excerpt_content_before' );

                     the_excerpt();

                     do_action( 'kadence_post_grid_excerpt_content_after' );
                    ?>
                </div>
                <footer>
                <?php 
                /**
                * @hooked virtue_post_footer_tags - 10
                */
                do_action( 'kadence_post_grid_excerpt_footer' );
                ?>
                </footer>
            </div><!-- Text size -->
            <?php 
               /**
               * 
               */
               do_action( 'kadence_post_grid_excerpt_after_footer' );
               ?>
        </div> <!-- Blog Item -->