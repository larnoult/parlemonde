<?php 

global $post, $virtue_premium; 

    $postsummery = get_post_meta( $post->ID, '_kad_post_summery', true );
    if(empty($postsummery) || $postsummery == 'default') {
        if(!empty($virtue_premium['post_summery_default'])) {
            $postsummery = $virtue_premium['post_summery_default'];
        } else {
            $postsummery = 'img_portrait';
        }
    }
    $image_height = apply_filters('kadence_blog_grid_image_height', null);
    if($postsummery == 'img_landscape') { ?>
        <div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in grid_item" itemscope="" itemtype="http://schema.org/BlogPosting">
				<?php
				$img_args = array(
					'width'           => 562,
					'height'          => $image_height,
					'crop'            => true,
					'class'           => 'iconhover',
					'alt'             => null,
					'id'              => get_post_thumbnail_id( $post->ID ),
					'placeholder'     => true,
					'schema'          => true,
					'intrinsic'       => true,
					'intrinsic_max'   => true,
				);
				?>
				<div class="imghoverclass img-margin-center">
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<?php virtue_print_full_image_output( $img_args ); ?>
					</a> 
				</div>
				<?php

    } else if ( 'img_portrait' == $postsummery ) { ?>
        <div id="post-<?php the_ID(); ?>" class="blog_item grid_item kt_item_fade_in portrait-grid" itemscope="" itemtype="http://schema.org/BlogPosting">
            <div class="rowtight">
			<?php 
			$img_args = array(
				'width'           => 364,
				'height'          => 364,
				'crop'            => true,
				'class'           => 'iconhover',
				'alt'             => null,
				'id'              => get_post_thumbnail_id( $post->ID ),
				'placeholder'     => true,
				'schema'          => true,
				'intrinsic'       => true,
				'intrinsic_max'   => true,
			);
			?>
			<div class="tcol-md-6 tcol-sm-12">
				<div class="imghoverclass">
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<?php virtue_print_full_image_output( $img_args ); ?>
					</a> 
				</div>
			</div>
			<?php
	} elseif($postsummery == 'slider_landscape') { ?>

        <div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in grid_item" itemscope="" itemtype="http://schema.org/BlogPosting">
                    <?php 
                    $image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
					virtue_build_slider( $post->ID, $image_gallery, 562, 300, 'image', 'kt-slider-same-image-ratio' );
                   
    } elseif($postsummery == 'slider_portrait') { ?>
		<div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in grid_item portrait-grid" itemscope="" itemtype="http://schema.org/BlogPosting">
			<div class="rowtight">
				<div class="tcol-lg-6 tcol-md-12">
					<?php 
					$image_gallery = get_post_meta( $post->ID, '_kad_image_gallery', true );
					virtue_build_slider ( $post->ID, $image_gallery, 364, 364, 'image', 'kt-slider-same-image-ratio' );
				?>
				</div>
    <?php 
    } elseif($postsummery == 'video') { ?>
        <div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in grid_item" itemscope="" itemtype="http://schema.org/BlogPosting">
            <div class="videofit">
                <?php $video = get_post_meta( $post->ID, '_kad_post_video', true ); 
                echo do_shortcode($video); ?>
            </div>
    <?php 
    } else {?>
		<div id="post-<?php the_ID(); ?>" class="blog_item kt_item_fade_in grid_item kt-no-post-summary" itemscope="" itemtype="http://schema.org/BlogPosting">
    <?php 
    } 

    if($postsummery == 'img_portrait' || $postsummery == 'slider_portrait') { ?>
        <div class="tcol-lg-6 tcol-md-12">
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
            * @hooked virtue_post_grid_excerpt_header_title - 10
            * @hooked virtue_post_grid_header_meta - 20
            */
            do_action( 'kadence_post_grid_excerpt_header' );
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
         <?php if($postsummery == 'img_portrait' || $postsummery == 'slider_portrait') { ?>
            </div> 
            </div>
        <?php } ?>
        <?php 
               /**
               * 
               */
               do_action( 'kadence_post_grid_excerpt_after_footer' );
               ?>
</div> <!-- Blog Item -->